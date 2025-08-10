import { DownloadCloud, Link } from 'lucide-react';
import { observer } from 'mobx-react-lite';
import { type FC, useEffect, useState } from 'react';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { Placeholder } from '@/Components/Placeholder/index.tsx';
import { OK } from '@/Components/Rest/http-status.ts';
import { UiError } from '@/Components/ui/error/index.tsx';
import styles from './index.module.scss';
import { ServerBenchmarkMyServer } from './my-server.tsx';
import stylesItem from './server-item.module.scss';
import { ServerBenchmarkItem } from './server-item.tsx';
import { ServerBenchmarkStore } from './store.ts';
import type { ServerBenchmarkProps } from './typings.ts';
export const ServerBenchmarkServers: FC = observer(() => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);
  const { servers, setServers, setMaxMarks, maxMarks } = ServerBenchmarkStore;
  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      const { data, status } =
        await serverFetch<ServerBenchmarkProps[]>('benchmarkServers');
      setLoading(false);
      if (!data?.length || status !== OK) {
        setError(true);
        return;
      }
      setError(false);
      let marks = 0;
      setServers(
        data
          .map((item) => {
            item.total = item.detail
              ? Object.values(item.detail).reduce((a, b) => a + b, 0)
              : 0;
            if (item.total > marks) {
              marks = item.total;
            }
            return item;
          })
          .toSorted((a, b) => (b?.total ?? 0) - (a?.total ?? 0))
      );
      setMaxMarks(marks);
    };
    fetchData();
  }, [setServers, setMaxMarks]);
  // const maxMarks = servers.reduce((a, b) => Math.max(a, b?.total ?? 0), 0)
  const results = servers.map(
    ({ name, url, date, probeUrl, binUrl, detail }) => {
      if (!detail) {
        return null;
      }
      const { cpu = 0, read = 0, write = 0 } = detail;
      const proberLink = probeUrl ? (
        <a
          className={stylesItem.link}
          href={probeUrl}
          rel="noreferrer"
          target="_blank"
          title={gettext('Visit probe page')}
        >
          <Link />
        </a>
      ) : (
        ''
      );
      const binLink = binUrl ? (
        <a
          className={stylesItem.link}
          href={binUrl}
          rel="noreferrer"
          target="_blank"
          title={gettext('Download speed test')}
        >
          <DownloadCloud />
        </a>
      ) : (
        ''
      );
      const title = (
        <a
          className={stylesItem.link}
          href={url}
          rel="noreferrer"
          target="_blank"
          title={gettext('Visit the official website')}
        >
          {name}
        </a>
      );
      return (
        <ServerBenchmarkItem
          date={date}
          header={
            <>
              {title}
              {proberLink}
              {binLink}
            </>
          }
          key={name}
          marks={{ cpu, read, write }}
          maxMarks={maxMarks}
        />
      );
    }
  );
  return (
    <div className={styles.servers}>
      <ServerBenchmarkMyServer />
      {loading
        ? // biome-ignore lint/suspicious/noArrayIndexKey: <explanation>
          [...new Array(5)].map((_, index) => <Placeholder key={index} />)
        : results}
      {error && (
        <UiError>{gettext('Can not fetch marks data from GitHub.')}</UiError>
      )}
    </div>
  );
});
