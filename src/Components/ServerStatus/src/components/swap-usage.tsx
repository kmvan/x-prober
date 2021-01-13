import CardGrid from '@/Card/src/components/card-grid'
import ProgressBar from '@/ProgressBar/src/components'
import React from 'react'
import store from '../stores'
import { gettext } from '@/Language/src'
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
