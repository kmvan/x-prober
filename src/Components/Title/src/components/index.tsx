import React, { Component } from 'react'
import styled from 'styled-components'
import { BORDER_RADOIS } from '~components/Config/src/index'
import { observer } from 'mobx-react'
import BootstrapStore from '~components/Bootstrap/src/stores'
import UpdaterStore from '~components/Updater/src/stores'
import UpdaterLink from '~components/Updater/src/components/updater-link'
import UpdaterChecker from '~components/Updater/src/components/updater-checker'

export const TitleH1 = styled.h1`
  background: #333;
  position: fixed;
  top: 1rem;
  left: 50%;
  transform: translateX(-50%);
  justify-content: center;
  text-align: center;
  margin: 0;
  min-width: 60vw;
  width: 50vw;
  font-size: 1rem;
  line-height: 1;
  border-radius: 0 0 ${BORDER_RADOIS} ${BORDER_RADOIS};
  box-shadow: inset 0 -7px 20px -7px rgba(0, 0, 0, 0.3);
  z-index: 10;
`
export const TitleLink = styled.a`
  display: block;
  padding: 0 1rem 1rem;
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
        <UpdaterChecker />
        <TitleH1>
          {UpdaterStore.newVersion ? (
            <UpdaterLink />
          ) : (
            <TitleLink href='https://github.com/kmvan/x-prober' target='_blank'>
              {`X Prober v${BootstrapStore.version}`}
            </TitleLink>
          )}
        </TitleH1>
      </>
    )
  }
}

export default Title
