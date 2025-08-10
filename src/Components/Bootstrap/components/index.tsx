import '@/Components/ColorScheme/components/config.scss';
import { type FC, useEffect, useState } from 'react';
import { ConfigStore } from '@/Components/Config/store.ts';
import { DatabaseStore } from '@/Components/Database/components/store.ts';
import { DiskUsageStore } from '@/Components/DiskUsage/components/store.ts';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { Footer } from '@/Components/Footer/components/index.tsx';
import { Header } from '@/Components/Header/components/index.tsx';
import { MyInfoStore } from '@/Components/MyInfo/components/store.ts';
import { NetworkStatsStore } from '@/Components/NetworkStats/components/store.ts';
import { NodesStore } from '@/Components/Nodes/components/store.ts';
import { PhpExtensionsStore } from '@/Components/PhpExtensions/components/store.ts';
import { PhpInfoStore } from '@/Components/PhpInfo/components/store.ts';
import type { PollDataProps } from '@/Components/Poll/components/typings.ts';
import { ServerInfoStore } from '@/Components/ServerInfo/components/store.ts';
import { ServerStatusStore } from '@/Components/ServerStatus/components/store.ts';
import { Toast } from '@/Components/Toast/components/index.tsx';
import { UserConfigStore } from '@/Components/UserConfig/store.ts';
import './global.scss';
import { Modules } from '@/Components/Module/components/index.tsx';
import { Nav } from '@/Components/Nav/components/index.tsx';
import { PollStore } from '@/Components/Poll/components/store.ts';
import { TemperatureSensorStore } from '@/Components/TemperatureSensor/components/store.ts';
import { BootstrapLoading } from './loading.tsx';
export const Bootstrap: FC = () => {
  const [loading, setLoading] = useState(true);
  useEffect(() => {
    let timeoutId: NodeJS.Timeout;
    let isMounted = true;
    const fetchData = async () => {
      try {
        const { data, status } = await serverFetch<PollDataProps>('poll');
        if (data && status === 200) {
          PollStore.setPollData(data);
          ConfigStore.setPollData(data?.config);
          UserConfigStore.setPollData(data?.userConfig);
          DatabaseStore.setPollData(data?.database);
          MyInfoStore.setPollData(data?.myInfo);
          PhpInfoStore.setPollData(data?.phpInfo);
          DiskUsageStore.setPollData(data?.diskUsage);
          PhpExtensionsStore.setPollData(data?.phpExtensions);
          NetworkStatsStore.setPollData(data?.networkStats);
          ServerStatusStore.setPollData(data?.serverStatus);
          ServerInfoStore.setPollData(data?.serverInfo);
          NodesStore.setPollData(data?.nodes);
          TemperatureSensorStore.setPollData(data?.temperatureSensor);
        } else {
          alert('Can not fetch data.');
        }
        if (loading) {
          setLoading(false);
        }
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
  }, [loading]);
  if (loading) {
    return <BootstrapLoading />;
  }
  return (
    <>
      <Header />
      <Modules />
      <Footer />
      <Nav />
      {/* <Forkme /> */}
      <Toast />
    </>
  );
};
