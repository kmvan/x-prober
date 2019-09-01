import React, { Component } from 'react'
import styled from 'styled-components'
import { BORDER_RADIUS, GUTTER, DARK_COLOR } from '~components/Config/src/index'
import { observer } from 'mobx-react'
import BootstrapStore from '~components/Bootstrap/src/stores'
import UpdaterStore from '~components/Updater/src/stores'
import UpdaterLink from '~components/Updater/src/components/updater-link'

export const TitleContainer = styled.h1`
  background: ${DARK_COLOR};
  position: fixed;
  top: ${GUTTER};
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
  box-shadow: inset 0 -7px 20px -7px rgba(0, 0, 0, 0.3);
  z-index: 10;
`
export const TitleLink = styled.a`
  display: block;
  padding: 0 ${GUTTER} ${GUTTER};
  color: #fff;
  &:hover {
    color: #fff;
  }
`
@observer
class Title extends Component {
  public render() {
    return (
      <>
        <TitleContainer>
          {UpdaterStore.newVersion ? (
            <UpdaterLink />
          ) : (
            <TitleLink href={BootstrapStore.appUrl} target='_blank'>
              {`X Prober v${BootstrapStore.version}`}
            </TitleLink>
          )}
        </TitleContainer>
      </>
    )
  }
}

export default Title
