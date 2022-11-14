import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { GridContainer } from '../../Grid/components/container'
import { gettext } from '../../Language'
import { ProgressBar } from '../../ProgressBar/components'
import { template } from '../../Utils/components/template'
import { TemperatureSensorStore } from '../stores'
export const TemperatureSensor: FC = observer(() => {
  const { itemsCount, items } = TemperatureSensorStore
  if (!itemsCount) {
    return null
  }
  return (
    <GridContainer>
      {items.map(({ id, name, celsius }) => (
        <CardGrid
          key={id}
          name={template(gettext('{{sensor}} temperature'), {
            sensor: name,
          })}
        >
          <ProgressBar
            value={celsius}
            max={150}
            isCapacity={false}
            percentTag='℃'
          />
        </CardGrid>
      ))}
    </GridContainer>
  )
})
