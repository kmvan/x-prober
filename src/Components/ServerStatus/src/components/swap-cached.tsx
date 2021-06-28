import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { CardGrid } from '../../../Card/src/components/card-grid'
import { gettext } from '../../../Language/src'
import { ProgressBar } from '../../../ProgressBar/src/components'
import { ServerStatusStore } from '../stores'
export const SwapCached: FC = observer(() => {
  const { max, value } = ServerStatusStore.swapCached
  if (!max) {
    return null
  }
  return (
    <CardGrid name={gettext('Swap cached')} tablet={[1, 1]}>
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
