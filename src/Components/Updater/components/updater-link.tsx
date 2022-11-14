import { observer } from 'mobx-react-lite'
import { FC, useCallback } from 'react'
import { serverFetch } from '../../Fetch/server-fetch'
import { gettext } from '../../Language'
import {
  INSUFFICIENT_STORAGE,
  INTERNAL_SERVER_ERROR,
  OK,
} from '../../Rest/http-status'
import { TitleLink } from '../../Title/components'
import { UpdaterStore } from '../stores'
export const UpdaterLink: FC = observer(() => {
  const onClick = useCallback(async () => {
    const { setIsUpdating, setIsUpdateError } = UpdaterStore
    setIsUpdating(true)
    const { status } = await serverFetch('update')
    switch (status) {
      case OK:
        window.location.reload()
        return
      case INSUFFICIENT_STORAGE:
      case INTERNAL_SERVER_ERROR:
        alert(
          gettext(
            'Can not update file, please check the server permissions and space.',
          ),
        )
        setIsUpdating(false)
        setIsUpdateError(true)
        return
      default:
    }
    alert(gettext('Network error, please try again later.'))
    setIsUpdating(false)
    setIsUpdateError(true)
  }, [])
  return (
    <TitleLink title={gettext('Click to update')} onClick={onClick}>
      {UpdaterStore.notiText}
    </TitleLink>
  )
})
