import { type FC, memo, useEffect, useState } from 'react';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { Placeholder } from '@/Components/Placeholder/index.tsx';
import type { PollDataProps } from '@/Components/Poll/components/typings.ts';
import { OK } from '@/Components/Rest/http-status.ts';
import { template } from '@/Components/Utils/components/template.ts';
import { UiError } from '@/Components/ui/error/index.tsx';
import { NodesCpu } from './cpu.tsx';
import { NodesDisk } from './disk.tsx';
import { NodesNetworkStats } from './network.tsx';
import styles from './node.module.scss';
import { NodesRam } from './ram.tsx';
import { NodesSwap } from './swap.tsx';
export const Node: FC<{ id: string }> = memo(({ id }) => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(0);
  const [pollData, setPollData] = useState<PollDataProps | null>(null);
  useEffect(() => {
    let timeoutId: NodeJS.Timeout;
    let isMounted = true;
    const fetchData = async () => {
      try {
        const { data, status } = await serverFetch<PollDataProps>(
          `nodes&nodeId=${id}`
        );
        if (loading) {
          setLoading(false);
        }
        if (!data || status !== OK) {
          setError(status);
          return;
        }
        setPollData(data);
      } finally {
        if (isMounted) {
          timeoutId = setTimeout(fetchData, 2000);
        }
      }
    };
    fetchData();
    return () => {
      isMounted = false;
      clearTimeout(timeoutId);
    };
  }, [id, loading]);
  const serverStatus = pollData?.serverStatus ?? null;
  const diskUsage = pollData?.diskUsage ?? null;
  const networkStats = pollData?.networkStats ?? null;
  const memRealUsage = pollData?.serverStatus?.memRealUsage ?? null;
  const memSwapUsage = pollData?.serverStatus?.swapUsage ?? null;
  const sysLoad = serverStatus?.sysLoad ?? [];
  const cpuUsage = serverStatus?.cpuUsage ?? null;
  return (
    <div className={styles.main}>
      <header className={styles.name}>{id}</header>
      {error !== 0 && (
        <UiError>{template(gettext('Error: {{error}}'), { error })}</UiError>
      )}
      {loading && <Placeholder height={10} />}
      {!loading && serverStatus && (
        <>
          {cpuUsage ? <NodesCpu cpuUsage={cpuUsage} sysLoad={sysLoad} /> : null}
          {memRealUsage ? <NodesRam data={memRealUsage} /> : null}
          {memSwapUsage ? <NodesSwap data={memSwapUsage} /> : null}
          {diskUsage ? <NodesDisk data={diskUsage} /> : null}
          {networkStats ? <NodesNetworkStats data={networkStats} /> : null}
        </>
      )}
    </div>
  );
});
