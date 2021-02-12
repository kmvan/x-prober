import serverFetch from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { useEffect, useState } from 'react'
export const useServerIp = (type: 4 | 6): string => {
  const [ip, setIp] = useState<string>(gettext('Loading...'))
  useEffect(() => {
    ;(async () => {
      const { data, status } = await serverFetch(`serverIpv${type}`)
      if (data?.ip && status === OK) {
        setIp(data.ip)
      } else {
        setIp('-')
      }
    })()
  })
  return ip
}
