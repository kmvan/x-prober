import { CardGrid } from '@/Card/src/components/card-grid'
import { Row } from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import { ProgressBar } from '@/ProgressBar/src/components'
import { template } from '@/Utils/src/components/template'
import { observer } from 'mobx-react-lite'
import React from 'react'
import { TemperatureSensorStore } from '../stores'
export const TemperatureSensor = observer(() => {
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
          tablet={[1, itemsCount === 1 ? 1 : 2]}>
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
