import React from 'react'
import styled, { keyframes } from 'styled-components'
import { GUTTER, BORDER_RADIUS, ANIMATION_DURATION_SC } from '@/Config/src'
import schemes from '../stores/colors'
import { rgba } from 'polished'
import store from '../stores'
import { observer } from 'mobx-react-lite'

const fadeIn = keyframes`
  from{
    transform: translate3d(0, -10%, 0);
    opacity: .5;
  }
  to{
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
`
interface StyledColorSchemeLinkProps {
  isActive: boolean
}
const StyledColorSchemeLink = styled.a<StyledColorSchemeLinkProps>`
  position: relative;
  flex: 0 0 calc(${GUTTER} * 2);
  height: ${GUTTER};
  margin-right: 1px;
  box-shadow: inset 0 14px 5px -8px ${rgba('#000', 0.1)};
  transition: ${ANIMATION_DURATION_SC}s;
  :first-child {
    border-radius: ${BORDER_RADIUS} 0 0 ${BORDER_RADIUS};
  }
  :last-child {
    border-radius: 0 ${BORDER_RADIUS} ${BORDER_RADIUS} 0;
    margin-right: 0;
  }
  :hover {
    transform: scale3d(1.5, 1.5, 1);
    z-index: 1;
  }
`
const StyledColorScheme = styled.div`
  display: flex;
  justify-content: center;
  margin: 0 0 calc(${GUTTER} * 2) 0;
  animation: ${fadeIn} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;
`

const ColorScheme = observer(() => {
  return (
    <StyledColorScheme>
      {Object.entries(schemes).map(([schemeId, { name, colorDark }]) => (
        <StyledColorSchemeLink
          isActive={schemeId === store.schemeId}
          title={name}
          key={schemeId}
          style={{ backgroundColor: colorDark }}
          onClick={() => store.setSchemeId(schemeId)}
        />
      ))}
    </StyledColorScheme>
  )
})

export default ColorScheme
