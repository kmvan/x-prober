import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { usePrevious } from '@/Components/Utils/components/use-previous.ts';
import { NetworkStatsConstants } from './constants.ts';
import styles from './index.module.scss';
import { NetworksStatsItem } from './item';
import { NetworkStatsStore } from './store';
export const NetworkStats: FC = observer(() => {
  const { sortNetworks, networksCount, timestamp } = NetworkStatsStore;
  const prevData = usePrevious({
    items: sortNetworks,
    timestamp,
  });
  if (!networksCount) {
    return null;
  }
  const seconds = timestamp - (prevData?.timestamp || timestamp);
  return (
    <ModuleItem id={NetworkStatsConstants.id} title={gettext('Network Stats')}>
      <div className={styles.container}>
        {sortNetworks.map(({ id, rx, tx }) => {
          if (!(rx || tx)) {
            return null;
          }
          const prevItem = (prevData?.items || sortNetworks).find(
            (item) => item.id === id
          );
          const prevRx = prevItem?.rx || 0;
          const prevTx = prevItem?.tx || 0;
          return (
            <NetworksStatsItem
              id={id}
              key={id}
              rateRx={(rx - prevRx) / seconds}
              rateTx={(tx - prevTx) / seconds}
              totalRx={rx}
              totalTx={tx}
            />
          );
        })}
      </div>
    </ModuleItem>
  );
});
