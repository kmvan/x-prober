import { CardGrid } from '@/Card/src/components/card-grid'
import { Row } from '@/Grid/src/components/row'
import { useIp } from '@/Hooks/src/useIp'
import { gettext } from '@/Language/src'
import { observer } from 'mobx-react-lite'
import React from 'react'
import { MyInfoStore } from '../stores'
import { ClientLocation } from './location'
export const MyInfo = observer(() => {
  const { conf } = MyInfoStore
  const { ip: ipv4, msg: ipv4Msg, isLoading: ipv4IsLoading } = useIp(4)
  const { ip: ipv6, msg: ipv6Msg, isLoading: ipv6IsLoading } = useIp(6)
  let myIpv4: string = ''
  let myIpv6: string = ''
  if (ipv4IsLoading) {
    myIpv4 = ipv4Msg
  } else if (ipv4) {
    myIpv4 = ipv4
  } else if (conf?.ipv4) {
    myIpv4 = conf.ipv4
  } else {
    myIpv4 = ipv4Msg
  }
  if (ipv6IsLoading) {
    myIpv6 = ipv6Msg
  } else if (ipv6) {
    myIpv6 = ipv6
  } else if (conf?.ipv6) {
    myIpv6 = conf.ipv6
  } else {
    myIpv6 = ipv6Msg
  }
  const items: any[] = [
    [gettext('My IPv4'), myIpv4],
    [gettext('My IPv6'), myIpv6],
    [gettext('My location (IPv4)'), <ClientLocation ip={ipv4 || conf?.ipv4} />],
    [gettext('My browser UA'), navigator.userAgent],
    [gettext('My browser languages (via JS)'), navigator.languages.join(',')],
    [gettext('My browser languages (via PHP)'), conf?.phpLanguage],
  ]
  return (
    <Row>
      {items.map(([name, content]: [string, string]) => {
        return (
          <CardGrid key={name} name={name} desktopLg={[1, 2]}>
            {content}
          </CardGrid>
        )
      })}
    </Row>
  )
})
