import React from 'react'
import styled from 'styled-components'
import { DARK_COLOR, GUTTER } from '~components/Config/src'
import { gettext } from '~components/Language/src'
import BootstrapStore from '~components/Bootstrap/src/stores'
import { device } from '~components/Style/src/components/devices'

const ForkmeLink = styled.a`
  position: absolute;
  top: 0;
  left: 0;
  background: ${DARK_COLOR};
  color: #fffc;
  font-family: Arial Black;
  padding: calc(${GUTTER} / 2) calc(${GUTTER} * 2);
  transform: rotate(-45deg) translate(-30%, -50%);
  font-size: 0.7rem;
  box-shadow: 0 3px 5px rgba(0, 0, 0, 0.3);

  @media ${device('tablet')} {
    font-size: 1rem;
    transform: rotate(-45deg) translate(-30%, -10%);
  }
  ::after,
  ::before {
    position: absolute;
    left: 0;
    top: 1px;
    height: 0.5px;
    width: 100%;
    background: linear-gradient(90deg, #fff0, #fff, #fff0);
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
    <ForkmeLink href={BootstrapStore.appUrl} target='_blank'>
      {gettext('STAR 🌟 ME')}
    </ForkmeLink>
  )
}

export default Forkme
