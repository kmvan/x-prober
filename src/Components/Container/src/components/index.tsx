import React, { HTMLAttributes } from 'react'
import styled from 'styled-components'
import { device } from '@/Style/src/components/devices'
import { GUTTER } from '@/Config/src'
const StyledContainer = styled.div`
  margin-left: auto;
  margin-right: auto;
  padding-left: calc(${GUTTER} / 2);
  padding-right: calc(${GUTTER} / 2);
  @media ${device('desktopSm')} {
    padding-left: ${GUTTER};
    padding-right: ${GUTTER};
  }
`
interface ContainerProps extends HTMLAttributes<HTMLDivElement> {}
const Container = (props: ContainerProps) => {
  return <StyledContainer {...props} />
}
export default Container
