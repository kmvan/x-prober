import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import Row from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import CardGrid from '@/Card/src/components/card-grid'
import ProgressBar from '@/ProgressBar/src/components'
import template from '@/Helper/src/components/template'

@observer
export default class TemperatureSensor extends Component {
  public render() {
    const { itemsCount, items } = store

    if (!itemsCount) {
      return null
    }

    return (
      <Row>
        {items.map(({ id, name, celsius }) => (
          <CardGrid
            key={id}
            name={template(gettext('${sensor} temperature'), {
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
  }
}
