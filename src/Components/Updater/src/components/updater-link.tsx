import React, { useCallback } from 'react'
import serverFetch from '@/Fetch/src/server-fetch'
import store from '../stores'
import { gettext } from '@/Language/src'
import { observer } from 'mobx-react-lite'
import { StyledTitleLink } from '@/Title/src/components'
import {
  OK,
  INSUFFICIENT_STORAGE,
  INTERNAL_SERVER_ERROR,
} from '@/Restful/src/http-status'
const UpdaterLink = observer(() => {
  const onClick = useCallback(async () => {
    const { setIsUpdating, setIsUpdateError } = store
    setIsUpdating(true)
    const { status } = await serverFetch('update')
    switch (status) {
      case OK:
        location.reload()
        return
      case INSUFFICIENT_STORAGE:
      case INTERNAL_SERVER_ERROR:
        alert(
          gettext(
            'Can not update file, please check the server permissions and space.'
          )
        )
        setIsUpdating(false)
        setIsUpdateError(true)
        return
    }
    alert(gettext('Network error, please try again later.'))
    setIsUpdating(false)
    setIsUpdateError(true)
  }, [])
  return (
    <StyledTitleLink title={gettext('Click to update')} onClick={onClick}>
      {store.notiText}
    </StyledTitleLink>
  )
})
export default UpdaterLink
