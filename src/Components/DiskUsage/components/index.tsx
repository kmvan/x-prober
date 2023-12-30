import { ProgressBar } from '@/Components/ProgressBar/components'
import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { GridContainer } from '../../Grid/components/container'
import { DiskUsageConstants } from '../constants'
import { DiskUsageItemProps } from './typings'
export const DiskUsage: FC = observer(() => {
  const { conf } = DiskUsageConstants
  const items = (conf?.items ?? []) as DiskUsageItemProps[]
  if (!items.length) {
    return null
  }
  return (
    <GridContainer>
      {items.map(({ id, free, total }) => (
        <CardGrid key={id} name={id}>
          <ProgressBar value={total - free} max={total} isCapacity />
        </CardGrid>
      ))}
    </GridContainer>
  )
})
