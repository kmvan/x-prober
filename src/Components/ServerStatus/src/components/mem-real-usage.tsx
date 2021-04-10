import { CardGrid } from '@/Card/src/components/card-grid'
import { gettext } from '@/Language/src'
import { ProgressBar } from '@/ProgressBar/src/components'
import { observer } from 'mobx-react-lite'
import React from 'react'
import { ServerStatusStore } from '../stores'
export const MemRealUsage = observer(() => {
  const { max, value } = ServerStatusStore.memRealUsage
  return (
    <CardGrid
      title={gettext(
        'Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.'
      )}
      name={gettext('Memory real usage')}
      tablet={[1, 1]}>
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
