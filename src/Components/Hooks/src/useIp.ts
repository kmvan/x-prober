import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { useEffect, useState } from 'react'
interface useIpProps {
  ip: string
  msg: string
  isLoading: boolean
}
export const useIp = (type: 4 | 6): useIpProps => {
  const [data, setData] = useState<useIpProps>({
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
  }, [])
  return data
}
