import { BootstrapStore } from '@/Bootstrap/src/stores'
import { ANIMATION_DURATION_SC, GUTTER } from '@/Config/src'
import { gettext } from '@/Language/src'
import { device } from '@/Style/src/components/devices'
import { rgba } from 'polished'
import React from 'react'
import styled, { keyframes } from 'styled-components'
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
  background: ${({ theme }) => theme['starMe.bg']};
  color: ${({ theme }) => theme['starMe.fg']};
  font-family: Arial Black;
  padding: calc(${GUTTER} / 3) calc(${GUTTER} * 3);
  font-size: calc(${GUTTER} * 0.7);
  box-shadow: 0 3px 5px ${({ theme }) => rgba(theme['starMe.bg'], 0.5)};
  z-index: 2;
  animation: ${slideIn} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;
  @media ${device('tablet')} {
    font-size: 1rem;
    top: calc(${GUTTER} / 2);
    left: calc(${GUTTER} / 2);
  }
  :hover {
    color: ${({ theme }) => theme['starMe.hover.fg']};
    background: ${({ theme }) => theme['starMe.hover.bg']};
    text-decoration: none;
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
      ${({ theme }) => rgba(theme['starMe.bg'], 0)},
      ${({ theme }) => theme['starMe.fg']},
      ${({ theme }) => rgba(theme['starMe.bg'], 0)}
    );
    content: '';
  }
  ::after {
    top: auto;
    bottom: 1px;
  }
`
export const Forkme = () => {
  return (
    <StyledForkmeLink href={BootstrapStore.appUrl} target='_blank' title='Fork'>
      {gettext('STAR 🌟 ME')}
    </StyledForkmeLink>
  )
}
