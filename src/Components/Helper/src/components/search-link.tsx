import React from 'react'
import styled from 'styled-components'
import { DARK_COLOR, GUTTER } from '~components/Config/src'

const A = styled.a`
  margin: 0 0.2rem 0.2rem 0;
  background: #3333330d;
  padding: 0 0.3rem;
  border-radius: ${GUTTER};
  font-family: consolas;
  :hover {
    background: ${DARK_COLOR};
    color: #fff;
    text-decoration: underline;
  }
`
const SearchLink = ({ keyword }: { keyword: string }) => {
  return (
    <A
      href={`https://www.google.com/search?q=php+${encodeURIComponent(
        keyword
      )}`}
      target='_blank'
      rel='nofollow'
    >
      {keyword}
    </A>
  )
}

export default SearchLink
