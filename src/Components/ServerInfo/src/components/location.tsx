import { serverFetch } from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { ToastStore } from '@/Toast/src/stores'
import { observer } from 'mobx-react-lite'
import React, { MouseEvent, useCallback, useState } from 'react'
import styled from 'styled-components'
import { locationProps } from '../stores'
interface ServerLocationProps {
  action: string
}
const StyledLocation = styled.a``
export const Location = observer(({ action }: ServerLocationProps) => {
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const [location, setLocation] = useState<locationProps | null>(null)
  const onClick = useCallback(
    async (e: MouseEvent<HTMLAnchorElement>) => {
      e.preventDefault()
      if (isLoading) {
        return
      }
      setIsLoading(true)
      const { data, status } = await serverFetch(action)
      setIsLoading(false)
      if (data && status === OK) {
        setLocation(data)
      } else {
        ToastStore.open(gettext('Can not fetch location.'))
      }
    },
    [isLoading]
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
