import { observer } from 'mobx-react-lite';
import type { FC, ReactNode } from 'react';
import { CardGroup } from '@/Components/Card/components/group.tsx';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { CardSingleColContainer } from '@/Components/Card/components/single-col-container.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { Location } from '@/Components/Location/components/index.tsx';
import { useIp } from '@/Components/Utils/components/use-ip.ts';
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
    <CardItem id={MyInfoConstants.id} title={gettext('My Info')}>
      <CardSingleColContainer>
        {items.map(([name, content]) => (
          <CardGroup key={name} label={name}>
            {content}
          </CardGroup>
        ))}
      </CardSingleColContainer>
    </CardItem>
  );
});
