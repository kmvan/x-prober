import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import styled from 'styled-components'
import store from '../stores'
import { GUTTER, COLOR_DARK_RGB } from '~components/Config/src'
import { device } from '~components/Style/src/components/devices'

const StyledGroup = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  @media ${device('tablet')} {
    justify-content: flex-start;
  }
`

const StyledGroupItem = styled.span`
  margin-right: 0.5rem;
  background: ${() =>
    `rgba(${COLOR_DARK_RGB[0]}, ${COLOR_DARK_RGB[1]}, ${COLOR_DARK_RGB[2]}, 0.75)`};
  color: #fff;
  padding: calc(${GUTTER} / 10) ${GUTTER};
  border-radius: 10rem;
  font-family: Arial Black;
  text-shadow: 0 1px 1px #000;
  box-shadow: inset 0 5px 10px rgba(0, 0, 0, 0.3);
  font-weight: 700;
`

@observer
class SystemLoad extends Component {
  public render() {
    return (
      <CardGrid title={gettext('System load')} tablet={[1, 1]}>
        <StyledGroup>
          {store.sysLoad.map((load, i) => (
            <StyledGroupItem key={i}>{load.toFixed(2)}</StyledGroupItem>
          ))}
        </StyledGroup>
      </CardGrid>
    )
  }
}

export default SystemLoad
