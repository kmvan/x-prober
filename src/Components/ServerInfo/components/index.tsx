import { observer } from 'mobx-react-lite';
import { type FC, memo, type ReactNode, useEffect } from 'react';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { Location } from '@/Components/Location/components/index.tsx';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { OK } from '@/Components/Rest/http-status.ts';
import { template } from '@/Components/Utils/components/template';
import { UiMultiColContainer } from '@/Components/ui/col/multi-container.tsx';
import { UiSingleColContainer } from '@/Components/ui/col/single-container.tsx';
import { ServerInfoConstants } from './constants.ts';
import { ServerInfoStore } from './store.ts';
import type { ServerInfoPollDataProps } from './typings.ts';

const ServerTime: FC<{
  serverUptime: ServerInfoPollDataProps['serverUptime'];
  serverTime: ServerInfoPollDataProps['serverTime'];
}> = observer(({ serverUptime, serverTime }) => {
  const { days, hours, mins, secs } = serverUptime;
  const uptime = template(
    gettext('{{days}}d {{hours}}h {{mins}}min {{secs}}s'),
    { days, hours, mins, secs }
  );
  const items = [
    [gettext('Time'), serverTime],
    [gettext('Uptime'), uptime],
  ];
  return (
    <>
      {items.map(([title, content]) => (
        <ModuleGroup key={title} label={title}>
          {content}
        </ModuleGroup>
      ))}
    </>
  );
});
const SingleItems: FC<{
  cpuModel: ServerInfoPollDataProps['cpuModel'];
  serverOs: ServerInfoPollDataProps['serverOs'];
  scriptPath: ServerInfoPollDataProps['scriptPath'];
  publicIpv4: string;
}> = memo(({ cpuModel, serverOs, scriptPath, publicIpv4 }) => {
  const items: [string, ReactNode][] = [
    [
      gettext('Location (IPv4)'),
      <Location ip={publicIpv4} key="serverLocalIpv4" />,
    ],
    [gettext('CPU model'), cpuModel ?? gettext('Unavailable')],
    [gettext('OS'), serverOs ?? gettext('Unavailable')],
    [gettext('Script path'), scriptPath ?? gettext('Unavailable')],
  ];
  return (
    <UiSingleColContainer>
      {items.map(([title, content]) => (
        <ModuleGroup key={title} label={title}>
          {content}
        </ModuleGroup>
      ))}
    </UiSingleColContainer>
  );
});
const MultiItems: FC<{
  serverName: ServerInfoPollDataProps['serverName'];
  serverSoftware: ServerInfoPollDataProps['serverSoftware'];
  publicIpv4: string;
  publicIpv6: string;
  localIpv4: string;
  localIpv6: string;
}> = memo(
  ({
    serverName,
    serverSoftware,
    publicIpv4,
    publicIpv6,
    localIpv4,
    localIpv6,
  }) => {
    const items: [string, ReactNode][] = [
      [gettext('Name'), serverName ?? gettext('Unavailable')],
      [gettext('Software'), serverSoftware ?? gettext('Unavailable')],
      [gettext('Public IPv4'), publicIpv4 || '-'],
      [gettext('Public IPv6 '), publicIpv6 || '-'],
      [gettext('Local IPv4'), localIpv4 || '-'],
      [gettext('Local IPv6 '), localIpv6 || '-'],
    ];
    return (
      <>
        {items.map(([title, content]) => (
          <ModuleGroup key={title} label={title}>
            {content}
          </ModuleGroup>
        ))}
      </>
    );
  }
);
export const ServerInfo: FC = observer(() => {
  const { pollData, publicIpv4, publicIpv6, setPublicIpv4, setPublicIpv6 } =
    ServerInfoStore;
  // fetch ipv4
  useEffect(() => {
    const fetchData = async () => {
      const { data, status } = await serverFetch<{ ip: string }>(
        'serverPublicIpv4'
      );
      if (data?.ip && status === OK) {
        setPublicIpv4(data.ip);
      }
    };
    fetchData();
  }, [setPublicIpv4]);
  // fetch ipv6
  useEffect(() => {
    const fetchData = async () => {
      const { data, status } = await serverFetch<{ ip: string }>(
        'serverPublicIpv6'
      );
      if (data?.ip && status === OK) {
        setPublicIpv6(data.ip);
      }
    };
    fetchData();
  }, [setPublicIpv6]);
  if (!pollData) {
    return null;
  }
  return (
    <ModuleItem id={ServerInfoConstants.id} title={gettext('Server Info')}>
      <UiMultiColContainer>
        <ServerTime
          serverTime={pollData.serverTime}
          serverUptime={pollData.serverUptime}
        />
        <MultiItems
          localIpv4={pollData.localIpv4}
          localIpv6={pollData.localIpv6}
          publicIpv4={publicIpv4}
          publicIpv6={publicIpv6}
          serverName={pollData.serverName}
          serverSoftware={pollData.serverSoftware}
        />
      </UiMultiColContainer>
      <SingleItems
        cpuModel={pollData.cpuModel}
        publicIpv4={publicIpv4}
        scriptPath={pollData.scriptPath}
        serverOs={pollData.serverOs}
      />
    </ModuleItem>
  );
});
