import { observer } from 'mobx-react-lite';
import { type FC, type RefObject, useCallback, useRef } from 'react';
import { Button } from '@/Components/Button/components/index.tsx';
import { ButtonStatus } from '@/Components/Button/components/typings.ts';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { OK } from '@/Components/Rest/http-status.ts';
import { calculateMdev } from '@/Components/Utils/components/mdev.ts';
import { template } from '@/Components/Utils/components/template.ts';
import { UiSingleColContainer } from '@/Components/ui/col/single-container.tsx';
import type { ServerToBrowserPingItemProps } from '../typings.ts';
import { PingStore } from './store.ts';
import styles from './style.module.scss';

const Results: FC = observer(() => {
  const { serverToBrowserPingItems } = PingStore;
  const count = serverToBrowserPingItems.length;
  const items = serverToBrowserPingItems.map(({ time }) => time);
  const avg = count ? (items.reduce((a, b) => a + b, 0) / count).toFixed(2) : 0;
  const max = count ? Math.max(...items) : 0;
  const min = count ? Math.min(...items) : 0;
  const mdev = calculateMdev(items).toFixed(2);
  return (
    <div className={styles.result}>
      {template(
        gettext(
          '{{times}} times, min/avg/max/mdev = {{min}}/{{avg}}/{{max}}/{{mdev}} ms'
        ),
        { times: count, min, max, avg, mdev }
      )}
    </div>
  );
});
const ResultContainer: FC<{
  refContainer: RefObject<HTMLUListElement | null>;
}> = observer(({ refContainer }) => {
  const { serverToBrowserPingItems } = PingStore;
  const count = serverToBrowserPingItems.length;
  return (
    <ModuleGroup label={gettext('Results')}>
      <div className={styles.resultContainer}>
        {!count && '-'}
        {Boolean(count) && (
          <ul className={styles.itemContainer} ref={refContainer}>
            {serverToBrowserPingItems.map(({ id, time }) => (
              <li key={id}>{`${time} ms`}</li>
            ))}
          </ul>
        )}
        {Boolean(count) && <Results />}
      </div>
    </ModuleGroup>
  );
});
export const PingServerToBrowser: FC = observer(() => {
  const {
    setIsPing,
    setIsPingServerToBrowser,
    addServerToBrowserPingItem,
    isPing,
    isPingServerToBrowser,
  } = PingStore;
  const refItemContainer = useRef<HTMLUListElement | null>(null);
  const refPingTimer = useRef<number>(0);
  const SERVER_TIME_MULTIPLIER = 1000;
  const TIMEOUT_TIMER_MS = 1000;
  const SCROLL_TIMER_MS = 100;
  const ping = useCallback(async (): Promise<void> => {
    const start = Date.now();
    const { data, status } =
      await serverFetch<ServerToBrowserPingItemProps>('ping');
    if (data?.time && status === OK) {
      const { id, time } = data;
      const end = Date.now();
      const serverTime = time * SERVER_TIME_MULTIPLIER;
      addServerToBrowserPingItem({
        id,
        time: Math.floor(end - start - serverTime),
      });
      setTimeout(() => {
        if (!refItemContainer.current) {
          return;
        }
        const st = refItemContainer.current.scrollTop;
        const sh = refItemContainer.current.scrollHeight;
        if (st < sh) {
          refItemContainer.current.scrollTop = sh;
        }
      }, SCROLL_TIMER_MS);
    }
  }, [addServerToBrowserPingItem]);
  const pingLoop = useCallback(async (): Promise<void> => {
    await ping();
    refPingTimer.current = window.setTimeout(async () => {
      await pingLoop();
    }, TIMEOUT_TIMER_MS);
  }, [ping]);
  const handlePing = useCallback(async () => {
    if (isPing || isPingServerToBrowser) {
      setIsPing(false);
      setIsPingServerToBrowser(false);
      clearTimeout(refPingTimer.current);
      return;
    }
    setIsPing(true);
    setIsPingServerToBrowser(true);
    await pingLoop();
  }, [
    isPing,
    isPingServerToBrowser,
    pingLoop,
    setIsPing,
    setIsPingServerToBrowser,
  ]);
  // const count = serverToBrowserPingItems.length;
  return (
    <UiSingleColContainer>
      <ModuleGroup label={gettext('Server ⇄ Browser')}>
        <Button
          onClick={handlePing}
          status={isPing ? ButtonStatus.Loading : ButtonStatus.Pointer}
        >
          {isPing ? gettext('Stop ping') : gettext('Start ping')}
        </Button>
      </ModuleGroup>
      <ResultContainer refContainer={refItemContainer} />
    </UiSingleColContainer>
  );
});
