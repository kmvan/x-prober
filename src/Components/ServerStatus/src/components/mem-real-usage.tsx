import CardGrid from '@/Card/src/components/card-grid'
import ProgressBar from '@/ProgressBar/src/components'
import React from 'react'
import store from '../stores'
import { gettext } from '@/Language/src'
import { observer } from 'mobx-react-lite'
const MemRealUsage = observer(() => {
  const { max, value } = store.memRealUsage
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
export default MemRealUsage
