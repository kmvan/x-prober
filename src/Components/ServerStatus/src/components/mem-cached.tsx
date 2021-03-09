import { CardGrid } from '@/Card/src/components/card-grid'
import { gettext } from '@/Language/src'
import { ProgressBar } from '@/ProgressBar/src/components'
import { observer } from 'mobx-react-lite'
import React from 'react'
import { ServerStatusStore } from '../stores'
export const MemCached = observer(() => {
  const { max, value } = ServerStatusStore.memCached
  return (
    <CardGrid
      title={gettext(
        'Cached memory is memory that Linux uses for disk caching. However, this doesn\'t count as "used" memory, since it will be freed when applications require it. Hence you don\'t have to worry if a large amount is being used.'
      )}
      name={gettext('Memory cached')}
      tablet={[1, 2]}>
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
