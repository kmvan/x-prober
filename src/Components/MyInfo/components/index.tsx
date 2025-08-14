import { observer } from 'mobx-react-lite';
import type { FC, ReactNode } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Location } from '@/Components/Location/components/index.tsx';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { useIp } from '@/Components/Utils/components/use-ip.ts';
import { UiSingleColContainer } from '@/Components/ui/col/single-container.tsx';
import { MyInfoConstants } from './constants.ts';
import { MyInfoStore } from './store.ts';
export const MyInfo: FC = observer(() => {
  const { pollData } = MyInfoStore;
  const { ip: ipv4, msg: ipv4Msg, isLoading: ipv4IsLoading } = useIp(4);
  const { ip: ipv6, msg: ipv6Msg, isLoading: ipv6IsLoading } = useIp(6);
  let myIpv4 = '';
  let myIpv6 = '';
  if (ipv4IsLoading) {
    myIpv4 = ipv4Msg;
  } else if (ipv4) {
    myIpv4 = ipv4;
  } else if (pollData?.ipv4) {
    myIpv4 = pollData.ipv4;
  } else {
    myIpv4 = ipv4Msg;
  }
  if (ipv6IsLoading) {
    myIpv6 = ipv6Msg;
  } else if (ipv6) {
    myIpv6 = ipv6;
  } else if (pollData?.ipv6) {
    myIpv6 = pollData.ipv6;
  } else {
    myIpv6 = ipv6Msg;
  }
  const items: [string, ReactNode][] = [
    [gettext('IPv4'), myIpv4],
    [gettext('IPv6'), myIpv6],
    [gettext('My location (IPv4)'), <Location ip={myIpv4} key="myLocalIpv4" />],
    [gettext('Browser UA'), navigator.userAgent],
    [gettext('Browser languages (via JS)'), navigator.languages.join(',')],
    [gettext('Browser languages (via PHP)'), pollData?.phpLanguage],
  ];
  if (!pollData) {
    return null;
  }
  return (
    <ModuleItem id={MyInfoConstants.id} title={gettext('My Info')}>
      <UiSingleColContainer>
        {items.map(([name, content]) => (
          <ModuleGroup key={name} label={name}>
            {content}
          </ModuleGroup>
        ))}
      </UiSingleColContainer>
    </ModuleItem>
  );
});
