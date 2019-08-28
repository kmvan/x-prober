import React from 'react'
import styled, { css } from 'styled-components'
import { size, device } from '~components/Style/src/components/devices'

const Div = styled.div`
  ${Object.entries(size).map(([id, px]) => {
    return css`
      @media ${device(id)} {
        max-width: ${px};
        margin-left: auto;
        margin-right: auto;
      }
    `
  })}
`

const Container = props => {
  return <Div {...props} />
}

export default Container
