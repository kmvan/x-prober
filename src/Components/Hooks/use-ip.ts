import { useEffect, useState } from 'react'
import { gettext } from '../Language'
import { OK } from '../Rest/http-status'
interface UseIpProps {
  ip: string
  msg: string
  isLoading: boolean
}
export const useIp = (type: 4 | 6): UseIpProps => {
  const [data, setData] = useState<UseIpProps>({
    ip: '',
    msg: gettext('Loading...'),
    isLoading: true,
  })
  useEffect(() => {
    ;(async () => {
      try {
        const res = await fetch(`https://ipv${type}.inn-studio.com/ip/?json`)
        const data = await res.json()
        if (data?.ip && res.status === OK) {
          setData({ ip: data.ip, msg: '', isLoading: false })
        } else {
          setData({
            ip: '',
            msg: gettext('Can not fetch IP'),
            isLoading: false,
          })
        }
      } catch (err) {
        setData({ ip: '', msg: gettext('Not support'), isLoading: false })
      }
    })()
  }, [type])
  return data
}
