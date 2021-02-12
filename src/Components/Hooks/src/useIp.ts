import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { useEffect, useState } from 'react'
interface useIpProps {
  ip: string
  msg: string
}
const useIp = (type: 4 | 6): useIpProps => {
  const [data, setData] = useState<useIpProps>({
    ip: '',
    msg: gettext('Loading...'),
  })
  useEffect(() => {
    ;(async () => {
      const res = await fetch(`https://ipv${type}.inn-studio.com/ip/`)
      const ip = await res.text()
      if (ip && res.status === OK) {
        setData({ ip, msg: '' })
      } else {
        setData({ ip: '', msg: gettext('Can not fetch IP.') })
      }
    })()
  }, [])
  return data
}

export default useIp
