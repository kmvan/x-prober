import FetchStore from '@/Fetch/src/stores'
import { gettext } from '@/Language/src'
import ProgressBar from '@/ProgressBar/src/components'
import { observer } from 'mobx-react-lite'
import React from 'react'
import store from '../stores'
const ServerDiskUsage = observer(() => {
  const { ID } = store
  const { isLoading, data } = FetchStore
  let {
    conf: {
      diskUsage: { value, max },
    },
  } = store
  if (!isLoading) {
    value = data?.[ID]?.diskUsage?.value
    max = data?.[ID]?.diskUsage?.max
  }
  if (!value || !max) {
    return <>{gettext('Unavailable')}</>
  }
  return <ProgressBar value={value} max={max} isCapacity />
})
export default ServerDiskUsage
