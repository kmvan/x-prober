import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { gettext } from '../../Language'
import { ProgressBar } from '../../ProgressBar/components'
import { ServerStatusStore } from '../stores'
export const SwapUsage: FC = observer(() => {
  const { max, value } = ServerStatusStore.swapUsage
  if (!max) {
    return null
  }
  return (
    <CardGrid name={gettext('Swap usage')}>
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
