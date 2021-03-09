import { ANIMATION_DURATION_SC, BORDER_RADIUS, GUTTER } from '@/Config/src'
import { observer } from 'mobx-react-lite'
import React from 'react'
import styled, { keyframes } from 'styled-components'
import { ColorSchemeStore } from '../stores'
import { colorSchemes } from '../stores/color-schemes'
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
  transition: ${ANIMATION_DURATION_SC}s;
  :first-child {
    border-top-left-radius: ${BORDER_RADIUS};
    border-bottom-left-radius: ${BORDER_RADIUS};
  }
  :last-child {
    border-top-right-radius: ${BORDER_RADIUS};
    border-bottom-right-radius: ${BORDER_RADIUS};
  }
  & + & {
    margin-left: 1px;
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
export const ColorScheme = observer(() => {
  return (
    <StyledColorScheme>
      {Object.entries(colorSchemes).map(([schemeId, { name, color }]) => (
        <StyledColorSchemeLink
          isActive={schemeId === ColorSchemeStore.schemeId}
          title={name}
          key={schemeId}
          style={{ background: color }}
          onClick={() => ColorSchemeStore.setSchemeId(schemeId)}
        />
      ))}
    </StyledColorScheme>
  )
})
