import React, { Component } from 'react'
import styled from 'styled-components'
import { BORDER_RADIUS, GUTTER, COLOR_DARK } from '~components/Config/src/index'
import { observer } from 'mobx-react'
import BootstrapStore from '~components/Bootstrap/src/stores'
import UpdaterStore from '~components/Updater/src/stores'
import UpdaterLink from '~components/Updater/src/components/updater-link'

export const StyledTitle = styled.h1`
  background: ${COLOR_DARK};
  position: fixed;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  justify-content: center;
  text-align: center;
  margin: 0;
  min-width: 60vw;
  width: 50vw;
  font-size: ${GUTTER};
  line-height: 1;
  border-radius: 0 0 ${BORDER_RADIUS} ${BORDER_RADIUS};
  box-shadow: inset 0 -3px 15px -5px rgba(0, 0, 0, 0.3);
  z-index: 10;
`

export const StyledTitleLink = styled.a`
  display: block;
  padding: ${GUTTER};
  color: #fff;

  :hover {
    color: #fff;
  }
`

@observer
class Title extends Component {
  public render() {
    return (
      <>
        <StyledTitle>
          {UpdaterStore.newVersion ? (
            <UpdaterLink />
          ) : (
            <StyledTitleLink href={BootstrapStore.appUrl} target='_blank'>
              {`X Prober v${BootstrapStore.version}`}
            </StyledTitleLink>
          )}
        </StyledTitle>
      </>
    )
  }
}

export default Title
