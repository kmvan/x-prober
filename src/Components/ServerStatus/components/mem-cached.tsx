import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { gettext } from '../../Language'
import { ProgressBar } from '../../ProgressBar/components'
import { ServerStatusStore } from '../stores'
export const MemCached: FC = observer(() => {
  const { max, value } = ServerStatusStore.memCached
  return (
    <CardGrid
      title={gettext(
        'Cached memory is memory that Linux uses for disk caching. However, this doesn\'t count as "used" memory, since it will be freed when applications require it. Hence you don\'t have to worry if a large amount is being used.',
      )}
      name={gettext('Memory cached')}
      lg={2}
    >
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
