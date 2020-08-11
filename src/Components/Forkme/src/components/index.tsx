import React from 'react'
import styled, { keyframes } from 'styled-components'
import { GUTTER, ANIMATION_DURATION_SC } from '~components/Config/src'
import { gettext } from '~components/Language/src'
import BootstrapStore from '~components/Bootstrap/src/stores'
import { device } from '~components/Style/src/components/devices'
import { rgba } from 'polished'

const slideIn = keyframes`
  from{
    transform: rotate(-45deg) translate3d(-28%, -270%, 0);
    @media ${device('tablet')} {
      transform: rotate(-45deg) translate3d(-28%, -250%, 0);
    }
  }
  to{
    transform: rotate(-45deg) translate3d(-28%, -70%, 0);
    @media ${device('tablet')} {
      transform: rotate(-45deg) translate3d(-28%, -50%, 0);
    }
  }
`
const StyledForkmeLink = styled.a`
  position: fixed;
  top: 0;
  left: 0;
  background: ${({ theme }) => theme.colorDark};
  color: ${({ theme }) => rgba(theme.colorGray, 0.85)};
  font-family: Arial Black;
  padding: calc(${GUTTER} / 3) calc(${GUTTER} * 3);
  font-size: calc(${GUTTER} * 0.7);
  box-shadow: 0 3px 5px ${({ theme }) => rgba(theme.colorDarkDeep, 0.3)};
  z-index: 2;
  animation: ${slideIn} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;

  @media ${device('tablet')} {
    font-size: 1rem;
    top: calc(${GUTTER} / 2);
    left: calc(${GUTTER} / 2);
  }
  ::after,
  ::before {
    position: absolute;
    left: 0;
    top: 1px;
    height: 0.5px;
    width: 100%;
    background: linear-gradient(
      90deg,
      ${({ theme }) => rgba(theme.colorGray, 0)},
      #fff,
      ${({ theme }) => rgba(theme.colorGray, 0)}
    );
    content: '';
  }
  ::after {
    top: auto;
    bottom: 1px;
  }
  :hover {
    color: ${({ theme }) => theme.colorGray};
    text-decoration: none;
  }
`

const Forkme = () => {
  return (
    <StyledForkmeLink href={BootstrapStore.appUrl} target='_blank' title='Fork'>
      {gettext('STAR 🌟 ME')}
    </StyledForkmeLink>
  )
}

export default Forkme
