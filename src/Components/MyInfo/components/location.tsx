import { observer } from 'mobx-react-lite'
import { FC, MouseEvent, useCallback, useState } from 'react'
import { serverFetch } from '../../Fetch/server-fetch'
import { gettext } from '../../Language'
import { OK } from '../../Rest/http-status'
import { LocationProps } from '../../ServerInfo/stores'
import { ToastStore } from '../../Toast/stores'
interface ClientLocationProps {
  ip: string
}
export const ClientLocation: FC<ClientLocationProps> = observer(({ ip }) => {
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const [location, setLocation] = useState<LocationProps | null>(null)
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
    [isLoading, ip],
  )
  const loadingText = isLoading ? gettext('Loading...') : ''
  let clickText = ''
  if (!isLoading) {
    clickText = location
      ? [location.flag, location.country, location.region, location.city]
          .filter((n) => Boolean(n))
          .join(', ')
      : gettext('👆 Click to fetch')
  }
  if (!ip) {
    return <>-</>
  }
  return (
    <a
      onClick={onClick}
      href='#'
      title={gettext(
        'The author only has 10,000 API requests per month, please do not abuse it.',
      )}
    >
      {loadingText}
      {clickText}
    </a>
  )
})
