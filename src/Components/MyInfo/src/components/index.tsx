import CardGrid from '@/Card/src/components/card-grid'
import Row from '@/Grid/src/components/row'
import useIp from '@/Hooks/src/useIp'
import { gettext } from '@/Language/src'
import { observer } from 'mobx-react-lite'
import React from 'react'
import store from '../stores'
import ClientLocation from './location'
const MyInfo = observer(() => {
  const { conf } = store
  const { ip: ipv4, msg: ipv4Msg } = useIp(4)
  const { ip: ipv6, msg: ipv6Msg } = useIp(6)
  const items: any[] = [
    [gettext('My IPv4'), `${ipv4Msg}${ipv4}`],
    [gettext('My IPv6'), `${ipv6Msg}${ipv6}`],
    [gettext('My location (IPv4)'), <ClientLocation ip={ipv4} />],
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
export default MyInfo
