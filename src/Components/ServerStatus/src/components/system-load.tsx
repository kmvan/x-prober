import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import styled from 'styled-components'
import store from '../stores'
import { GUTTER } from '~components/Config/src'

const GroupContainer = styled.div`
  display: flex;
  align-items: center;
`

const GroupItem = styled.span`
  margin-right: 0.5rem;
  background: #33333380;
  color: #fff;
  padding: calc(${GUTTER} / 10) ${GUTTER};
  border-radius: 10rem;
  font-family: Arial Black;
  text-shadow: 0 1px 1px #333;
  box-shadow: inset 0 5px 10px rgba(0, 0, 0, 0.3);
  font-weight: 700;
`

@observer
class SystemLoad extends Component {
  public render() {
    return (
      <CardGrid title={gettext('System load')} tablet={[1, 1]}>
        <GroupContainer>
          {store.sysLoad.map((load, i) => (
            <GroupItem key={i}>{load.toFixed(2)}</GroupItem>
          ))}
        </GroupContainer>
      </CardGrid>
    )
  }
}

export default SystemLoad
