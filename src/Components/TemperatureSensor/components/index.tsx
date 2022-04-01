import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { Row } from '../../Grid/components/row'
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
    <Row>
      {items.map(({ id, name, celsius }) => (
        <CardGrid
          key={id}
          name={template(gettext('{{sensor}} temperature'), {
            sensor: name,
          })}
          tablet={[1, itemsCount === 1 ? 1 : 2]}
        >
          <ProgressBar
            value={celsius}
            max={150}
            isCapacity={false}
            percentTag='℃'
          />
        </CardGrid>
      ))}
    </Row>
  )
})
