import React from 'react'
import styled from 'styled-components'
import { COLOR_DARK, GUTTER } from '~components/Config/src'
import { gettext } from '~components/Language/src'
import BootstrapStore from '~components/Bootstrap/src/stores'
import { device } from '~components/Style/src/components/devices'

const StyledForkmeLink = styled.a`
  position: fixed;
  top: 0;
  left: 0;
  background: ${COLOR_DARK};
  color: rgba(255, 255, 255, 0.85);
  font-family: Arial Black;
  padding: calc(${GUTTER} / 3) calc(${GUTTER} * 3);
  transform: rotate(-45deg) translate(-28%, -70%);
  font-size: calc(${GUTTER} * 0.7);
  box-shadow: 0 3px 5px rgba(0, 0, 0, 0.3);
  z-index: 2;

  @media ${device('tablet')} {
    font-size: 1rem;
    transform: rotate(-45deg) translate(-28%, -50%);
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
      rgba(255, 255, 255, 0),
      #fff,
      rgba(255, 255, 255, 0)
    );
    content: '';
  }
  ::after {
    top: auto;
    bottom: 1px;
  }
  :hover {
    color: #fff;
    text-decoration: none;
  }
`

const Forkme = () => {
  return (
    <StyledForkmeLink href={BootstrapStore.appUrl} target='_blank'>
      {gettext('STAR 🌟 ME')}
    </StyledForkmeLink>
  )
}

export default Forkme
