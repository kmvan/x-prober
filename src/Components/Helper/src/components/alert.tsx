import React, { ReactNode } from 'react'
import styled from 'styled-components'
import { GUTTER } from '@/Config/src'
import { rgba } from 'polished'

interface AlertContainerProps {
  isSuccess: boolean
  withIcon: boolean
}

const StyledAlert = styled.div<AlertContainerProps>`
  display: inline-flex;
  border-radius: ${GUTTER};
  align-items: center;
  justify-content: center;
  font-family: Arial Black;
  font-weight: bolder;
  min-width: 2em;
  color: ${({ theme }) => theme.colorGray};
  box-shadow: inset 0 5px 10px ${({ theme }) => rgba(theme.colorDark, 0.3)};
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};
  padding: 0 0.5rem;
  white-space: nowrap;
  cursor: pointer;
  background: ${({ isSuccess }) => (isSuccess ? '#00e800' : '#c1c1c1')};

  :active {
    transform: scale3d(0.9, 0.9, 1);
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
