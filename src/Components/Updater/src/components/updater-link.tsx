import { serverFetch } from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import {
  INSUFFICIENT_STORAGE,
  INTERNAL_SERVER_ERROR,
  OK,
} from '@/Restful/src/http-status'
import { StyledTitleLink } from '@/Title/src/components'
import { observer } from 'mobx-react-lite'
import React, { useCallback } from 'react'
import { UpdaterStore } from '../stores'
export const UpdaterLink = observer(() => {
  const onClick = useCallback(async () => {
    const { setIsUpdating, setIsUpdateError } = UpdaterStore
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
      {UpdaterStore.notiText}
    </StyledTitleLink>
  )
})
