import React, { ReactNode } from 'react'
import styled, { css } from 'styled-components'
import { device, size } from '~components/Style/src/components/devices'
import { GUTTER } from '~components/Config/src'

interface ISizes {
  mobileSm?: [number, number]
  mobileMd?: [number, number]
  mobileLg?: [number, number]
  tablet?: [number, number]
  desktopSm?: [number, number]
  desktopMd?: [number, number]
  desktopLg?: [number, number]
}

export interface IGrid extends ISizes {
  children: ReactNode
}

const createCss = (types: ISizes) => {
  const style = Object.entries(types).map(([id, sizes]) => {
    if (!size[id]) {
      return ''
    }

    if (!sizes || !sizes.length) {
      return ''
    }

    const [span, col] = sizes

    return css`
      @media ${device(id)} {
        flex: 0 0 ${(span / col) * 100}%;
      }
    `
  })

  return style
}

export interface IGridStyle {
  types: ISizes
}

export const GridStyle = styled.div<IGridStyle>`
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  flex: 0 0 100%;
  ${props => createCss(props.types)}
`

const Grid = ({
  mobileSm,
  mobileMd,
  mobileLg,
  tablet,
  desktopSm,
  desktopMd,
  desktopLg,
  children,
}: IGrid) => {
  const types = {
    mobileSm,
    mobileMd,
    mobileLg,
    tablet,
    desktopSm,
    desktopMd,
    desktopLg,
  }

  return <GridStyle types={types}>{children}</GridStyle>
}

export default Grid
