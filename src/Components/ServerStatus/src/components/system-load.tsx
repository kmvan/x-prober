import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import styled from 'styled-components'
import store from '../stores'
import {
  GUTTER,
  COLOR_DARK_RGB,
  COLOR_DARK,
  TEXT_SHADOW_WITH_DARK_BG,
  COLOR_GRAY,
} from '~components/Config/src'
import { device } from '~components/Style/src/components/devices'
import { rgba } from 'polished'
import { template } from 'lodash-es'

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
  color: ${COLOR_GRAY};
  padding: calc(${GUTTER} / 10) calc(${GUTTER} / 1.5);
  border-radius: 10rem;
  font-family: Arial Black;
  text-shadow: ${TEXT_SHADOW_WITH_DARK_BG};
  box-shadow: inset 0 5px 10px ${rgba(COLOR_DARK, 0.3)};
  font-weight: 700;

  @media ${device('tablet')} {
    padding: calc(${GUTTER} / 10) ${GUTTER};
  }
`

@observer
class SystemLoad extends Component {
  public render() {
    const { sysLoad } = store
    const minutes = [1, 5, 15]
    const loadHuman = sysLoad.map((load, i) => {
      return {
        id: `${minutes[i]}minAvg`,
        load,
        text: template(gettext('<%= minute %> minute average'))({
          minute: minutes[i],
        }),
      }
    })

    return (
      <CardGrid name={gettext('System load')} tablet={[1, 1]}>
        <StyledGroup>
          {loadHuman.map(({ id, load, text }) => (
            <StyledGroupItem key={id} title={text}>
              {load.toFixed(2)}
            </StyledGroupItem>
          ))}
        </StyledGroup>
      </CardGrid>
    )
  }
}

export default SystemLoad
