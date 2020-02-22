import React, { ReactNode } from 'react'
import styled from 'styled-components'
import {
  GUTTER,
  COLOR_DARK,
  TEXT_SHADOW_WITH_DARK_BG,
  COLOR_GRAY,
} from '~components/Config/src'
import { rgba } from 'polished'

interface AlertContainerProps {
  isSuccess: boolean
  withIcon: boolean
}

const StyledAlert = styled.div<AlertContainerProps>`
  display: inline-flex;
  border-radius: ${GUTTER};
  align-items: center;
  justify-content:center;
  font-family: Arial Black;
  font-weight: bolder;
  min-width: 2em;
  color:${COLOR_GRAY};
  box-shadow: inset 0 5px 10px ${rgba(COLOR_DARK, 0.3)};
  text-shadow: ${TEXT_SHADOW_WITH_DARK_BG};
  padding: 0 0.5rem;
  white-space: nowrap;
  cursor: pointer;
  background: ${({ isSuccess }) => (isSuccess ? '#00e800' : '#c1c1c1')};

  :active{
    transform: scale3d(.9,.9,1);
    background: ${({ isSuccess }) => (isSuccess ? '#0bbfc3' : '#ff4747')};
  }

  ::before {
    content: '${({ isSuccess, withIcon }) => {
      if (!withIcon) {
        return ''
      }
      return isSuccess ? '✓' : '×'
    }}';
  }
`

export interface AlertProps {
  isSuccess: boolean
  msg?: ReactNode
}

const Alert = ({ isSuccess, msg = '' }: AlertProps) => {
  return (
    <StyledAlert isSuccess={isSuccess} withIcon={!msg}>
      {msg}
    </StyledAlert>
  )
}

export default Alert
