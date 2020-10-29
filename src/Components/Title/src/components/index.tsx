import React from 'react'
import styled, { keyframes } from 'styled-components'
import {
  BORDER_RADIUS,
  GUTTER,
  ANIMATION_DURATION_SC,
} from '@/Config/src/index'
import BootstrapStore from '@/Bootstrap/src/stores'
import UpdaterStore from '@/Updater/src/stores'
import UpdaterLink from '@/Updater/src/components/updater-link'
import { rgba } from 'polished'
import { observer } from 'mobx-react-lite'

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

const Title = observer(() => {
  const { appUrl, appName, version } = BootstrapStore

  return (
    <StyledTitle>
      {UpdaterStore.newVersion ? (
        <UpdaterLink />
      ) : (
        <StyledTitleLink href={appUrl} target='_blank'>
          {`${appName} v${version}`}
        </StyledTitleLink>
      )}
    </StyledTitle>
  )
})

export default Title
