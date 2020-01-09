import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import Row from '~components/Grid/src/components/row'
import { gettext } from '~components/Language/src'
import CardGrid from '~components/Card/src/components/card-grid'
import styled from 'styled-components'
import { device } from '~components/Style/src/components/devices'

const StyledTemperatureSensorName = styled.div`
  text-align: center;
`
const StyledTemperatureSensorValue = styled.div`
  font-family: 'Arial Black';
`
const StyledTemperatureSensorNumber = styled.span`
  white-space: nowrap;
`
const StyledTemperatureSensorCelsius = styled.span`
  display: none;

  @media ${device('mobileMd')} {
    display: inline;
  }
`

@observer
class TemperatureSensor extends Component {
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
            title={
              <StyledTemperatureSensorName>{name}</StyledTemperatureSensorName>
            }
            tablet={[1, 4]}
            mobileSm={[1, 2]}
          >
            <StyledTemperatureSensorValue>
              <StyledTemperatureSensorNumber>
                {celsius}
              </StyledTemperatureSensorNumber>
              <StyledTemperatureSensorCelsius>
                {'℃'}
              </StyledTemperatureSensorCelsius>
            </StyledTemperatureSensorValue>
          </CardGrid>
        ))}
      </Row>
    )
  }
}

export default TemperatureSensor
