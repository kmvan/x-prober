import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { gettext } from '../../Language'
import { ProgressBar } from '../../ProgressBar/components'
import { ServerStatusStore } from '../stores'
export const MemRealUsage: FC = observer(() => {
  const { max, value } = ServerStatusStore.memRealUsage
  return (
    <CardGrid
      title={gettext(
        'Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.',
      )}
      name={gettext('Memory real usage')}
    >
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
