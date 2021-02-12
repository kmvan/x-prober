import serverFetch from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import ToastStore from '@/Toast/src/stores'
import { observer } from 'mobx-react-lite'
import React, { MouseEvent, useCallback, useState } from 'react'
import styled from 'styled-components'
import store, { locationProps } from '../stores'
const StyledServerLocation = styled.a``
const ServerLocation = observer(() => {
  const { serverIpv4 } = store
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const [location, setLocation] = useState<locationProps | null>(null)
  const onClick = useCallback(
    (e: MouseEvent<HTMLAnchorElement>) => {
      ;(async () => {
        e.preventDefault()
        if (isLoading) {
          return
        }
        setIsLoading(true)
        const { data, status } = await serverFetch('serverLocationIpv4')
        setIsLoading(false)
        if (data && status === OK) {
          setLocation(data)
        } else {
          ToastStore.open(gettext('Can not fetch location.'))
        }
      })()
    },
    [serverIpv4, isLoading]
  )
  const loadingText = isLoading ? gettext('Loading...') : ''
  let clickText = ''
  if (!isLoading) {
    clickText = location
      ? `${location.flag} ${location.country}, ${location.region}, ${location.city}`
      : gettext('👆 Click to fetch')
  }
  return (
    <StyledServerLocation
      onClick={onClick}
      title={gettext(
        'The author only has 10,000 API requests per month, please do not abuse it.'
      )}>
      {loadingText}
      {clickText}
    </StyledServerLocation>
  )
})
export default ServerLocation
