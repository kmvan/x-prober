import React from 'react'
import CardGrid from '@/Card/src/components/card-grid'
import { gettext } from '@/Language/src'
import store from '../stores'
import ProgressBar from '@/ProgressBar/src/components'
import { observer } from 'mobx-react-lite'

const SwapUsage = observer(() => {
  const { max, value } = store.swapUsage

  if (!max) {
    return null
  }

  return (
    <CardGrid name={gettext('Swap usage')} tablet={[1, 1]}>
      <ProgressBar value={value} max={max} isCapacity />
    </CardGrid>
  )
})

export default SwapUsage
