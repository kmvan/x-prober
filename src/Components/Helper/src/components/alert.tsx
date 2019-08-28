import React, { ReactNode } from 'react'
import styled from 'styled-components'
import rgbaToHex from './rgbToHex'

interface IDiv {
  isSuccess: boolean
}

const Msg = styled.span`
  margin-left: 0.5rem;
`

const Div = styled.div<IDiv>`
  display: inline-flex;
  border-radius: 1rem;
  align-items: center;
  justify-content:center;
  font-family: Arial Black;
  font-weight: bolder;
  min-width: 2em;
  color: #fff;
  box-shadow: inset 0 5px 10px #${rgbaToHex(0, 0, 0, 0.3)};
  text-shadow: 0 1px 1px #333;
  padding: 0 0.5rem;
  cursor: pointer;
  background: ${({ isSuccess }) => (isSuccess ? '#00e800' : '#c1c1c1')};
  :active{
    transform: scale3d(.9,.9,1);
    background: ${({ isSuccess }) => (isSuccess ? '#0bbfc3' : '#ff4747')};
  }
  ::before {
    content: '${({ isSuccess }) => (isSuccess ? '✓' : '×')}';
  }
`

export interface IAlert {
  isSuccess: boolean
  msg?: ReactNode
}

const Alert = ({ isSuccess, msg = '' }: IAlert) => {
  return <Div isSuccess={isSuccess}>{msg ? <Msg>{msg}</Msg> : ''}</Div>
}

export default Alert
