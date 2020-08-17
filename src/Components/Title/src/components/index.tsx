import React, { Component } from 'react'
import styled, { keyframes } from 'styled-components'
import {
  BORDER_RADIUS,
  GUTTER,
  ANIMATION_DURATION_SC,
} from '~components/Config/src/index'
import { observer } from 'mobx-react'
import BootstrapStore from '~components/Bootstrap/src/stores'
import UpdaterStore from '~components/Updater/src/stores'
import UpdaterLink from '~components/Updater/src/components/updater-link'
import { rgba } from 'polished'

const slideDown = keyframes`
  from{
    transform: translate3d(-50%, -100%, 0);
  }
  to{
    transform: translate3d(-50%, 0, 0);
  }
`
export const StyledTitle = styled.h1`
  background: ${({ theme }) => theme.colorDark};
  position: fixed;
  top: 0;
  left: 50%;
  justify-content: center;
  text-align: center;
  margin: 0;
  min-width: 60vw;
  width: 50vw;
  font-size: ${GUTTER};
  line-height: 1;
  border-radius: 0 0 ${BORDER_RADIUS} ${BORDER_RADIUS};
  box-shadow: inset 0 -11px 10px -14px
      ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)},
    0 5px 10px ${({ theme }) => rgba(theme.colorDarkDeep, 0.1)};
  z-index: 10;
  animation: ${slideDown} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;
`
export const StyledTitleLink = styled.a`
  display: block;
  padding: ${GUTTER};
  color: ${({ theme }) => theme.colorGray};

  :hover {
    color: ${({ theme }) => theme.colorGray};
  }
`

@observer
export default class Title extends Component {
  public render() {
    return (
      <StyledTitle>
        {UpdaterStore.newVersion ? (
          <UpdaterLink />
        ) : (
          <StyledTitleLink href={BootstrapStore.appUrl} target='_blank'>
            {`X Prober v${BootstrapStore.version}`}
          </StyledTitleLink>
        )}
      </StyledTitle>
    )
  }
}
