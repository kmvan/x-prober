import { serverFetch } from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { locationProps } from '@/ServerInfo/src/stores'
import { ToastStore } from '@/Toast/src/stores'
import { observer } from 'mobx-react-lite'
import React, { MouseEvent, useCallback, useState } from 'react'
import styled from 'styled-components'
interface ClientLocationProps {
  ip: string
}
const StyledLocation = styled.a``
export const ClientLocation = observer(({ ip }: ClientLocationProps) => {
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const [location, setLocation] = useState<locationProps | null>(null)
  const onClick = useCallback(
    async (e: MouseEvent<HTMLAnchorElement>) => {
      e.preventDefault()
      if (isLoading) {
        return
      }
      setIsLoading(true)
      const { data, status } = await serverFetch(`clientLocationIpv4&ip=${ip}`)
      setIsLoading(false)
      if (data && status === OK) {
        setLocation(data)
      } else {
        ToastStore.open(gettext('Can not fetch location.'))
      }
    },
    [isLoading, ip]
  )
  const loadingText = isLoading ? gettext('Loading...') : ''
  let clickText = ''
  if (!isLoading) {
    clickText = location
      ? [location.flag, location.country, location.region, location.city]
          .filter((n) => !!n)
          .join(', ')
      : gettext('👆 Click to fetch')
  }
  if (!ip) {
    return <>{'-'}</>
  }
  return (
    <StyledLocation
      onClick={onClick}
      title={gettext(
        'The author only has 10,000 API requests per month, please do not abuse it.'
      )}>
      {loadingText}
      {clickText}
    </StyledLocation>
  )
})
