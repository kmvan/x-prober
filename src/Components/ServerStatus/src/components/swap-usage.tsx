import { CardGrid } from '@/Card/src/components/card-grid'
import { gettext } from '@/Language/src'
import { ProgressBar } from '@/ProgressBar/src/components'
import { observer } from 'mobx-react-lite'
import React from 'react'
import { ServerStatusStore } from '../stores'
export const SwapUsage = observer(() => {
  const { max, value } = ServerStatusStore.swapUsage
  if (!max) {
    return null
  }
  return (
    <CardGrid name={gettext('Swap usage')} tablet={[1, 1]}>
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})
