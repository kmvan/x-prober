import React, { ReactNode } from 'react'
import styled from 'styled-components'
import { GUTTER, COLOR_DARK } from '~components/Config/src'

interface IAlertContainer {
  isSuccess: boolean
  withIcon: boolean
}

const StyledAlert = styled.div<IAlertContainer>`
  display: inline-flex;
  border-radius: ${GUTTER};
  align-items: center;
  justify-content:center;
  font-family: Arial Black;
  font-weight: bolder;
  min-width: 2em;
  color: #fff;
  box-shadow: inset 0 5px 10px rgba(0, 0, 0, 0.3);
  text-shadow: 0 1px 1px ${COLOR_DARK};
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

export interface IAlert {
  isSuccess: boolean
  msg?: ReactNode
}

const Alert = ({ isSuccess, msg = '' }: IAlert) => {
  return (
    <StyledAlert isSuccess={isSuccess} withIcon={!msg}>
      {msg}
    </StyledAlert>
  )
}

export default Alert
